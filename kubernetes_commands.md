Kubernetes logs between time
```
kubectl logs [pod] --since-time 2024-01-04T11:50:00Z --timestamps -n [namespace] | awk -F"T" '$2 >= "11:50:00" && $2 <= "12:10:00"'
```
