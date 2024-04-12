echo "CURRENT_UID: ${CURRENT_UID}"
pushd "$(dirname $0)"

if [ -z $CURRENT_UID ]
    then
    set -e
    set -o allexport
    source .env
    test -f .env.local && source .env.local
    set +o allexport
    # set -x #echo on
    
    mkdir -p $DIR_SQL_DATA
    mkdir -p $DIR_SQL_DUMP

    export CURRENT_USER=$(whoami)
    export CURRENT_UID=$(id -u)
    export CURRENT_GID=$(id -g)
    export COMPOSE_DOCKER_CLI_BUILD=1
    export DOCKER_BUILDKIT=1
fi

function docker_do() {
    docker compose \
        -f docker-compose.yml \
        -f docker-compose.$ENV.yml \
        $@
}

function docker_admin_exec() {
    docker_do "
        exec $APP_CONTAINER \
        $@"
}

function docker_exec() {
    appContainer=$APP_CONTAINER

    if [ "$#" -ne 1 ]; then
        appContainer=$1
        shift
    fi

    docker_do "
        exec $appContainer \
        su-exec $CURRENT_USER:$CURRENT_USER \
        $@"
}

function clone_git() {
    repo=$1
    folder=$2
    clone=0

    if [ -d $folder ]; then
        git -C $folder rev-parse 2>/dev/null
        
        if [ $? -ne 0 ]; then
            clone=1
        fi
    else
        clone=1
    fi

    if [ $clone -ne 0 ] ; then
        git clone $repo $folder
    fi
} 
