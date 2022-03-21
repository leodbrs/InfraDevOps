#!/bin/bash

WORKDIR=`pwd`
cd "$WORKDIR"/Vagrant/
vagrant up
cd "$WORKDIR"/Ansible
ansible-playbook -i \
    "$WORKDIR"/Vagrant/.vagrant/provisioners/ansible/inventory/vagrant_ansible_inventory \
    setup-cluster.yml