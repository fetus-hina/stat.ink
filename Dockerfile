FROM centos:centos7
MAINTAINER AIZAWA Hina <hina@bouhime.com>

ADD docker/nginx/nginx.repo /etc/yum.repos.d/
RUN yum update -y && \
    yum install -y \
        curl nginx scl-utils \
        http://ftp.tsukuba.wide.ad.jp/Linux/fedora/epel/7/x86_64/e/epel-release-7-5.noarch.rpm \
        https://www.softwarecollections.org/en/scls/remi/php56more/epel-7-x86_64/download/remi-php56more-epel-7-x86_64.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/git19/epel-7-x86_64/download/rhscl-git19-epel-7-x86_64.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/nodejs010/epel-7-x86_64/download/rhscl-nodejs010-epel-7-x86_64.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/rh-php56/epel-7-x86_64/download/rhscl-rh-php56-epel-7-x86_64.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/rh-postgresql94/epel-7-x86_64/download/rhscl-rh-postgresql94-epel-7-x86_64.noarch.rpm \
        https://www.softwarecollections.org/en/scls/rhscl/v8314/epel-7-x86_64/download/rhscl-v8314-epel-7-x86_64.noarch.rpm \
            && \
    yum install -y \
        ImageMagick \
        git19-git \
        jpegoptim \
        libwebp-tools \
        more-php56-php-mcrypt \
        more-php56-php-pecl-msgpack \
        nodejs010-npm \
        patch \
        pngcrush \
        rh-php56-php-cli \
        rh-php56-php-fpm \
        rh-php56-php-gd \
        rh-php56-php-intl \
        rh-php56-php-mbstring \
        rh-php56-php-opcache \
        rh-php56-php-pdo \
        rh-php56-php-pecl-jsonc \
        rh-php56-php-pgsql \
        rh-php56-php-xml \
        rh-postgresql94-postgresql \
        rh-postgresql94-postgresql-server \
        supervisor \
            && \
    yum clean all && \
    ln -s /var/opt/rh/rh-postgresql94/lib/pgsql /var/lib/pgsql/rh-postgresql94 && \
    useradd statink && \
    chmod 701 /home/statink

ADD docker/env/scl-env.sh /etc/profile.d/
ADD docker/supervisor/* /etc/supervisord.d/
ADD . /home/statink/stat.ink
RUN chown -R statink:statink /home/statink/stat.ink

USER statink
RUN cd ~statink/stat.ink && bash -c 'source /etc/profile.d/scl-env.sh && make clean && make init'

USER postgres
RUN scl enable rh-postgresql94 'initdb --pgdata=/var/opt/rh/rh-postgresql94/lib/pgsql/data --encoding=UNICODE --locale=en_US.UTF8'
ADD docker/database/pg_hba.conf /var/opt/rh/rh-postgresql94/lib/pgsql/data/pg_hba.conf
ADD docker/database/password.php /var/opt/rh/rh-postgresql94/lib/pgsql/
RUN scl enable rh-postgresql94 rh-php56 ' \
        /opt/rh/rh-postgresql94/root/usr/libexec/postgresql-ctl start -D /var/opt/rh/rh-postgresql94/lib/pgsql/data -s -w && \
        createuser -DRS statink && \
        createdb -E UNICODE -O statink -T template0 statink && \
        php /var/opt/rh/rh-postgresql94/lib/pgsql/password.php && \
        /opt/rh/rh-postgresql94/root/usr/libexec/postgresql-ctl stop -D /var/opt/rh/rh-postgresql94/lib/pgsql/data -s -m fast'

USER root
RUN cd ~statink/stat.ink && \
    bash -c ' \
        source /etc/profile.d/scl-env.sh && \
        su postgres -c "/opt/rh/rh-postgresql94/root/usr/libexec/postgresql-ctl start -D /var/opt/rh/rh-postgresql94/lib/pgsql/data -s -w" && \
        su statink  -c "make" && \
        su postgres -c "/opt/rh/rh-postgresql94/root/usr/libexec/postgresql-ctl stop -D /var/opt/rh/rh-postgresql94/lib/pgsql/data -s -m fast"'

ADD docker/php/php-config.diff /tmp/
RUN patch -p1 -d /etc/opt/rh/rh-php56 < /tmp/php-config.diff && rm /tmp/php-config.diff

ADD docker/nginx/default.conf /etc/nginx/conf.d/

CMD /usr/bin/supervisord
EXPOSE 80
