pipeline {
  agent {
    label 'equipo01'
  }
  stages {
    stage('Docker huerfanos') {
      steps {
        sh 'docker container rm --force $(docker ps -a --quiet) || true'
        sh 'docker volume prune --force || true'
        sh 'docker image prune -f'
      }
    }
    stage('Imagen Docker') {
      environment {
        DOCKERHUB = credentials('jenkinsudc-dockerhub-account')
        // Solucion sencilla para obtener el SHA1 del commit en pipelines
        // https://issues.jenkins-ci.org/browse/JENKINS-44449
        GIT_COMMIT_SHORT = sh(
          script: "printf \$(git rev-parse --short ${GIT_COMMIT})",
          returnStdout: true
        )
      }
      steps {
        sh 'docker-compose build'
      }
      post {
        success {
          // Bug reportado en golang-docker-credential-helpers que no permite
          // autenticar el cliente Docker a un registry cuando se instala el
          // paquete docker-compose en distribuciones basadas en Debian
          sh 'sudo apt-get remove golang-docker-credential-helpers -y -q'
          sh 'docker login --username $DOCKERHUB_USR --password $DOCKERHUB_PSW'
          sh 'sudo apt-get install docker-compose -y -q'
          sh 'docker tag equipo01-backend-php:latest $DOCKERHUB_USR/equipo01-backend-php:latest'
          sh 'docker tag equipo01-backend-php:latest $DOCKERHUB_USR/equipo01-backend-php:$GIT_COMMIT_SHORT'
          sh 'docker tag equipo01-backend-php:latest $DOCKERHUB_USR/equipo01-backend-php:$BUILD_NUMBER-$GIT_COMMIT_SHORT'
          sh 'docker push $DOCKERHUB_USR/equipo01-backend-php:latest'
          sh 'docker push $DOCKERHUB_USR/equipo01-backend-php:$GIT_COMMIT_SHORT'
          sh 'docker push $DOCKERHUB_USR/equipo01-backend-php:$BUILD_NUMBER-$GIT_COMMIT_SHORT'
        }
        failure {
          sh 'docker image prune -f'
        }
      }
    }
    stage('Tests') {
      steps {
        sh 'docker-compose -f docker-compose.test.yml up'
        junit(testResults: 'reports/*.xml', allowEmptyResults: true)
        sh 'docker-compose down -v --remove-orphans'
        sh 'docker system prune --volumes --force'
      }
    }
    stage('Deploy') {
      post {
        failure {
          echo 'A execution failed'
          sh 'docker-compose down -v --remove-orphans || true'
          sh 'docker system prune --volumes --force || true'
          sh 'docker rmi --force $(docker images --quiet)'
        }
      }
      steps {
        sh 'docker-compose up -d'
      }
    }
  }
  environment {
    DOCKERHUB = credentials('jenkinsudc-dockerhub-account')
  }
}