#!/bin/bash

cd Vagrant/
vagrant up
cd ../Ansible
ansible-playbook -i \
    ../Vagrant/.vagrant/provisioners/ansible/inventory/vagrant_ansible_inventory \
    setup-cluster.yml \