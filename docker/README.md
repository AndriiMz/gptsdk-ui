## Deploy to dockerhub
1. docker login
1. docker build -t andriimz/gptsdk-ui .
1. docker tag andriimz/:<tag> andriimz/gptsdk-ui:<tag>
1. docker push andriimz/gptsdk-ui:<tag>
