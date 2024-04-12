#!/bin/bash
source "$(dirname $0)"/_init.sh

docker_exec $@ "bash"

popd
