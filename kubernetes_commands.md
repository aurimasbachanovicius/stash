Kubernetes logs between time
```
kubectl logs [pod] --since-time 2024-01-04T11:50:00Z --timestamps -n [namespace] | awk -F"T" '$2 >= "11:50:00" && $2 <= "12:10:00"'
```
Alias for faster switching between context azure aks
```
alias k[application][environment]="az account set --subscription [subsid] && kubectl config use-context [context name]"
```

Alias for broadcasting all pods from same app from kubernetes:
```
alias broadcastall='k logs -f --timestamps -l app.kubernetes.io/instance=[deployment name] --all-containers=true --max-log-requests=6'
```

