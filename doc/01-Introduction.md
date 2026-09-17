Introduction
============

Top Level View is a hierarchy based status view for Icinga Web 2.

You can define a hierarchical structure containing hosts, services and host groups.
This view presents you an overview of the overall status of the sub-hierarchies.

These hierarchical structures are configured in a YAML file. Example:

```yaml
name: My View
children:
- name: Section 1
  children:
  - name: Tile 1
    children:
    - host: localhost
    - host: localhost
      service: disk
    - host: anotherhost
      service: ssh
  - name: Tile 2
    hostgroup: linux-servers
  - servicegroup: http
```

The files are stored in `/etc/icingaweb2/modules/toplevelview/views/`.
Example: `/etc/icingaweb2/modules/toplevelview/views/myview.yml`.

**Hint:** Top Level View can use additional status logic for its views, see later chapters on details.

## Installation

Top Level View is a module for Icinga Web 2, and can be installed via Git or a tarball.

The only other requirement is the PHP YAML extension.

**Hint**: Make sure to reload your web server after installing the extension.

    # on RHEL and compatible
    dnf install php-pecl-yaml
    systemctl reload httpd.service

    # on Debian / Ubuntu
    apt-get install php-yaml
    systemctl reload apache2.service

You can download the latest released tarball from [GitHub](https://github.com/Icinga/icingaweb2-module-toplevelview/releases).

    tar xf icingaweb2-module-toplevelview-1.0.0.tar.gz
    mv icingaweb2-module-toplevelview-1.0.0/ /usr/share/icingaweb2/modules/toplevelview

Or - if you prefer - use Git.

    git clone https://github.com/Icinga/icingaweb2-module-toplevelview.git \
      /usr/share/icingaweb2/modules/toplevelview
    git checkout v1.0.0

Then enable the module in the web interface, or via CLI:

    icingacli module enable toplevelview

**Hint:** This module is capable of strict Content Security Policy (CSP).

**Note:** the module is currently not yet available via https://packages.netways.de/ due to some unresolved dependencies.

## Permissions and Restrictions

Top Level View offers the following permissions and restrictions:

* Permission `toplevelview/edit`, allow the user to edit Top Level Views
* Restriction `toplevelview/filter/edit`, restrict edit rights to Views that match the filter (comma-separated values)
* Restriction `toplevelview/filter/views`, restrict access to Views that match the filter (comma-separated values)

Restrictions use the filename of the YAML files. Example: `toplevelview/filter/edit: myview,anotherview`

```bash
ls -l /etc/icingaweb2/modules/toplevelview/views/

myview.yml
anotherview.yml
someview.yml
```

**Hint:** Commas in filenames should be avoided.
