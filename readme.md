Elgar's Coding Orchestra
=====================

Our totally unnamed project is still pretty sparse. We currently have a server set up at spe.sneeza.me, and all members of our group have an account on that server with sudo access. Your default password will be 'password' which you can change by running the passwd command.

Project Page
---------------------

We currently have a public facing website located at:

```
http://project.spe.sneeza.me/
```

Our API
---------------------

You can find detailed documentation about how to use the API on [Mashape](https://www.mashape.com/sneeza/project-rainhawk#!documentation). The API is free to use while it's still in active pre-release development. Mashape have many wrappers for different languages which can be used to make calls to the API using an authorization key, so they'll help you get started.

We are currently in the process of developing more complex and native wrapper classes for different languages, to interface with the API directly and take care of all the error handling for you. Please check back here for more info in the near future.

Developing With Vagrant
---------------------

In order to develop with Vagrant, you need to do the following:

1. Install [Vagrant](http://www.vagrantup.com/) and [VirtualBox](https://www.virtualbox.org/wiki/Downloads).

2. Clone this repository somewhere on your machine.

    ```bash
    git clone https://github.com/zacoppotamus/elgarscodingorchestra.git
    ```

3. Navigate into the cloned directory and start Vagrant. This may take a while to run, so you can move to the next steps while it's downloading all of the necessary files.

    ```bash
    cd elgarscodingorchestra && vagrant up
    ```

4. Add the following records to your `/etc/hosts` or `%systemroot%\system32\drivers\etc\hosts` file:

    ```bash
    127.0.0.1    rainhawk.dev    api.rainhawk.dev
    ```

5. Once step 3 has completed provisioning, navigate your browser to [rainhawk.dev:8080](http://rainhawk.dev:8080) and you'll be able to access the project website.

6. To get access to the dev machine, use the following command from anywhere in the directory. This will create an SSH tunnel into the virtual machine, where `/vagrant` is a syncronised folder to the git directory.

    ```bash
    vagrant ssh
    ```
