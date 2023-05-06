#!/bin/bash

declare -A servers=(
  ["192.168.191.215"]="FrontEnd"
  ["192.168.191.111"]="Messaging"
  ["192.168.191.240"]="Database"
  ["192.168.191.67"]="BackEnd"
)

for ip in "${!servers[@]}"; do
  if ping -c1 "$ip" >/dev/null 2>&1; then
          echo -e "\e[1;34m${servers[$ip]}:\t\e[0m\e[1;33m(${ip})\t\e[0m is \e[1;32mONLINE(✔)\e[0m"
  else
          echo -e "\e[1;34m${servers[$ip]}:\t\e[0m\e[1;33m(${ip})\t\e[0m is \e[1;31mOFFLINE(✘)\e[0m"
  fi
