#!/bin/bash

# ********* Build image ************** #

version="1.01"

rm -rf ./build_app/

#app source code --- PYTHON
mkdir -p ./build_app/

tar cf - --wildcards --ignore-case \
    --exclude='*docker*' \
    --exclude='.env*' \
    --exclude='.gitignore' \
    --exclude='*.txt' \
    --exclude='data' \
    -C ./../.. . | tar xf - -C ./build_app

echo "${version}" > ./build_app/app/app.version

# execute permission
chmod +x ./docker-deploy-entrypoint-custom

docker build --force-rm -f Dockerfile -t obe:${version} .

# #clean
# rm -rf ./build_app