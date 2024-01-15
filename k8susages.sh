#!/bin/bash

# Description:
# This script is designed to gather and display resource usage (CPU and Memory) for Kubernetes Pods on a specified node or across all nodes.
# It provides a detailed view of each running pod's resource consumption and sums up the total usage.
# This script is particularly useful for administrators and developers who need to monitor and optimize resource utilization in a Kubernetes environment.
#
# Usage:
# ./k8susages.sh <node>    - Displays resource usage for all running pods on the specified node.
# ./k8susages.sh all       - Displays resource usage for all running pods on all nodes.
# Optional flag --show-error can be used to display errors encountered during the execution.
#
# Dependencies:
# This script depends on 'kubectl', which must be installed and configured to communicate with your Kubernetes cluster.
#
# Output:
# The script outputs a list of running pods along with their CPU and memory usage. It also provides a total sum of memory and CPU usage,
# as well as a percentage of memory utilization with respect to the node's allocatable memory.

node_name=$1

show_error=0
for arg in "$@"; do
    [[ $arg == "--show-error" ]] && show_error=1 && break
done

if [ -z "$node_name" ]; then
  printf "Usage: k8susages.sh <node> -- (get specific node usages)\nUsage: k8susages.sh all -- (get all nodes usages)\n" \
    | column -t -s "--"
  exit 1
fi

if ! command -v kubectl &>/dev/null; then
    echo "Error: kubectl is not installed."
    exit 1
fi

if [ "$node_name" == "all" ]; then
  kubectl get nodes -o wide |\
    grep "Ready" |\
    awk '{print $1}' |\
    while read -r node; do $0 "$node"; done
  exit 0
fi;

progress_count=0
lines=$(kubectl get pods -o wide -A | grep "$node_name" | grep -c "Running")

show_progress() {
  local total=$1
  local symbol=$2
  printf "\r\033[KProcessing: "
  ((progress_count++))
  for ((i = 0; i <= progress_count; i++)); do
        printf "%s" "$symbol"
  done
  printf " (%d/%d)" "$progress_count" "$total"
}

kubectl get pods -o wide -A |\
  grep "$node_name" |\
  grep "Running" |\
  awk '{print $1 " " $2}' |\
  {
    output=""
    error_output=""
    while read -r ns pod; do

      if result=$(kubectl top pod --no-headers -n "$ns" "$pod" 2>&1); then
        output="$output\n$result"
        show_progress "$lines" "#"
      else
        error_output="$error_output\n$result"
        show_progress "$lines" "."
      fi;
    done

    printf "\n -- %s -- \n" "$node_name"
    echo -e "$output" | column -t | sort -k3,3nr

    sum_mem_usage=$(echo -e "$output" | column -t | awk '{sum += $3} END {print sum}')
    max_mem_usage=$(kubectl describe node "$node_name" | grep -A6 "Allocatable" | awk '/memory/{print $2}' | awk '{printf "%.0f\n", $1 / 1024}')
    percentage=$(( (sum_mem_usage * 100) / max_mem_usage ))
    sum_cpu_usage=$(echo -e "$output" | column -t | awk '{sum += $2} END {print sum "m"}')
    percentage_symbol="%"

    printf "\nSum of RAM usage || %s/%sMi (%s%s)\nSum of CPU usage || %s" \
      "$sum_mem_usage" "$max_mem_usage" "$percentage" "$percentage_symbol" "$sum_cpu_usage" \
      | column -t -s "||"

    if [ "$show_error" == 1 ]; then
      printf "\n\n -- Errors -- \n"
      echo -e "$error_output" | column -t
    fi;
  }