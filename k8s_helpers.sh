# Get logs from a pod between two timestamps
kubectl logs podname --since-time 2024-01-04T11:50:00Z --timestamps -n default | awk -F"T" '$2 >= "11:50:00" && $2 <= "12:10:00"'
