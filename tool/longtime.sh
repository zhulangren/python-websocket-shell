#!/bin/bash

min=1
max=5
while [ $min -le $max ]
do
    echo "$min"
    sleep 2
    min=`expr $min + 1`
done 

