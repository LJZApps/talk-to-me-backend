#!/bin/bash
source $(dirname $0)/_init.sh

docker_do $@

./_out.sh
