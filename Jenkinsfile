pipeline {
  agent {
    label 'equipo01'
  }
  environment {
    DOCKERHUB = credentials('jenkinsudc-dockerhub-account')
  }
  stages {
    stage('Kill everything') {
      steps {
        sh 'docker-compose down -v --remove-orphans || true'
        sh 'docker system prune --volumes --force || true'
      }
    }
    stage('Build image') {
      post {
        success {
          sh 'docker login --username $DOCKERHUB_USR --password $DOCKERHUB_PSW'
          sh 'docker tag equipo01-backend-php:latest $DOCKERHUB_USR/equipo01-backend-php:latest'
          sh 'docker push $DOCKERHUB_USR/equipo01-backend-php:latest'
        }
        failure {
          sh 'docker system prune --volumes --force || true'
        }
      }
      steps {
        sh 'docker-compose build'
      }
    }
    stage('Tests') {
      steps {
        sh 'docker-compose -f docker-compose.tests.yml up'
        sh 'id'
        script {
          publishHTML([
            allowMissing: false,
            alwaysLinkToLastBuild: true,
            keepAll: true,
            reportDir: 'reports/html/',
            reportFiles: 'index.html',
            reportName: 'Coverage report in HTML',
            reportTitles: ''
          ])
        }
        junit(testResults: 'reports/*.xml', allowEmptyResults: true)
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
}
