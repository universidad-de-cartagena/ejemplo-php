<?php

/**
 * OpenTelemetry configuration for Laravel application.
 *
 * Add to app/Providers/AppServiceProvider.php boot():
 *     require_once __DIR__ . '/../../observability/otel-config.php';
 *
 * Or add to bootstrap/app.php before app creation.
 */

use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Instrumentation\Http;
use OpenTelemetry\Contrib\Otlp\Grpc\SpanExporterFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\TracerProviderInterface;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\ResourceConstants;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\API\Trace\SpanKind;

if (!function_exists('setupOpenTelemetry')) {
    function setupOpenTelemetry(): TracerProviderInterface
    {
        $serviceName = getenv('OTEL_SERVICE_NAME') ?: 'ejemplo-php';
        $serviceVersion = getenv('OTEL_SERVICE_VERSION') ?: '1.0.0';
        $environment = getenv('OTEL_ENVIRONMENT') ?: (getenv('APP_ENV') ?: 'development');
        $otlpEndpoint = getenv('OTEL_EXPORTER_OTLP_ENDPOINT') ?: 'http://otel-collector:4317';

        $resource = ResourceInfo::create([
            ResourceConstants::SERVICE_NAME => $serviceName,
            ResourceConstants::SERVICE_VERSION => $serviceVersion,
            'deployment.environment' => $environment,
            'service.namespace' => 'ejemplo-php',
        ]);

        $exporter = SpanExporter::fromConnectionString($otlpEndpoint);

        $tracerProvider = TracerProvider::builder()
            ->setResource($resource)
            ->addSpanProcessor(new BatchSpanProcessor($exporter))
            ->build();

        $GLOBALS['tracer_provider'] = $tracerProvider;

        return $tracerProvider;
    }
}

if (!function_exists('getTracer')) {
    function getTracer(string $name = 'ejemplo-php'): \OpenTelemetry\API\Trace\TracerInterface
    {
        return $GLOBALS['tracer_provider']->getTracer($name);
    }
}
