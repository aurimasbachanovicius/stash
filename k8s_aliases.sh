#!/bin/bash

alias k=kubectl
alias knamespace='kubectl config set-context --current --namespace '
alias kappprod="az account set --subscription [subsid] && kubectl config use-context [context name]"
alias broadcastall='k logs -f --timestamps -l app.kubernetes.io/instance=[deployment name] --all-containers=true --max-log-requests=6'