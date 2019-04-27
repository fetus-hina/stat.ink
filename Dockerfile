FROM centos:7
MAINTAINER AIZAWA Hina <hina@bouhime.com>

ADD docker/rpm-gpg/ /etc/pki/rpm-gpg/
ADD docker/jp3cki/jp3cki.repo /etc/yum.repos.d/

RUN rpm --import \
        /etc/pki/rpm-gpg/RPM-GPG-KEY-CentOS-7 \
        /etc/pki/rpm-gpg/RPM-GPG-KEY-CentOS-SIG-SCLo \
        /etc/pki/rpm-gpg/RPM-GPG-KEY-EPEL-7 \
        /etc/pki/rpm-gpg/RPM-GPG-KEY-JP3CKI \
        /etc/pki/rpm-gpg/RPM-GPG-KEY-remi \
            && \
    yum update -y && \
    yum install -y \
        centos-release-scl-rh \
        curl \
        epel-release \
        gnupg2 \
        scl-utils \
        http://rpms.famillecollet.com/enterprise/7/safe/x86_64/remi-release-7.2-1.el7.remi.noarch.rpm \
            && \
    curl -sS https://rpm.nodesource.com/setup_7.x | bash && \
    yum install -y \
        ImageMagick \
        brotli \
        diff \
        gcc-c++ \
        gearmand \
        git19-git \
        gzip \
        h2o \
        jpegoptim \
        make \
        nodejs \
        patch \
        php73-php-cli \
        php73-php-fpm \
        php73-php-gd \
        php73-php-intl \
        php73-php-json \
        php73-php-mbstring \
        php73-php-mcrypt \
        php73-php-opcache \
        php73-php-pdo \
        php73-php-pecl-gearman \
        php73-php-pecl-msgpack \
        php73-php-pecl-zip \
        php73-php-pgsql \
        php73-php-process \
        php73-php-xml \
        php73-runtime \
        rh-postgresql95-postgresql \
        rh-postgresql95-postgresql-server \
        supervisor \
        unzip \
            && \
    yum clean all && \
    ln -s /var/opt/rh/rh-postgresql95/lib/pgsql /var/lib/pgsql/rh-postgresql95 && \
    useradd statink && \
    chmod 701 /home/statink

ADD docker/env/scl-env.sh /etc/profile.d/
ADD docker/supervisor/* /etc/supervisord.d/
ADD docker/jp3cki/0xF6B887CD.asc /home/statink/
ADD . /home/statink/stat.ink
RUN chown -R statink:statink /home/statink/stat.ink

USER statink
RUN gpg --import /home/statink/0xF6B887CD.asc && gpg --refresh-keys
RUN cd ~statink/stat.ink && bash -c 'source /etc/profile.d/scl-env.sh && make clean && make init-by-archive && rm -f runtime/vendor-archive/*'

USER postgres
RUN scl enable rh-postgresql95 'initdb --pgdata=/var/opt/rh/rh-postgresql95/lib/pgsql/data --encoding=UNICODE --locale=en_US.UTF8'
ADD docker/database/pg_hba.conf /var/opt/rh/rh-postgresql95/lib/pgsql/data/pg_hba.conf
ADD docker/database/password.php /var/opt/rh/rh-postgresql95/lib/pgsql/
RUN scl enable rh-postgresql95 php73 ' \
        /opt/rh/rh-postgresql95/root/usr/libexec/postgresql-ctl start -D /var/opt/rh/rh-postgresql95/lib/pgsql/data -s -w && \
        createuser -DRS statink && \
        createdb -E UNICODE -O statink -T template0 statink && \
        php /var/opt/rh/rh-postgresql95/lib/pgsql/password.php && \
        /opt/rh/rh-postgresql95/root/usr/libexec/postgresql-ctl stop -D /var/opt/rh/rh-postgresql95/lib/pgsql/data -s -m fast'

USER root
RUN cd ~statink/stat.ink && \
    bash -c ' \
        source /etc/profile.d/scl-env.sh && \
        su postgres -c "/opt/rh/rh-postgresql95/root/usr/libexec/postgresql-ctl start -D /var/opt/rh/rh-postgresql95/lib/pgsql/data -s -w" && \
        su statink  -c "make" && \
        su postgres -c "/opt/rh/rh-postgresql95/root/usr/libexec/postgresql-ctl stop -D /var/opt/rh/rh-postgresql95/lib/pgsql/data -s -m fast"'

ADD docker/php/php-config.diff /tmp/
RUN patch -p1 -d /etc/opt/remi/php73 < /tmp/php-config.diff && rm /tmp/php-config.diff

ADD docker/h2o/h2o.conf /etc/h2o/h2o.conf

CMD /usr/bin/supervisord
EXPOSE 80
