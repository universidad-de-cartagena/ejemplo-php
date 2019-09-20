pipeline {
  agent {
    label 'equipo01'
  }
  stages {
    stage('Kill everything') {
      steps {
        sh 'docker-compose down -v --remove-orphans || true'
        sh 'docker container kill $(docker ps -a -q) || true'
        sh 'docker rmi --force $(docker images -a -q) || true'
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