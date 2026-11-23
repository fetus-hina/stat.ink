FROM docker.io/rockylinux/rockylinux:10
LABEL org.opencontainers.image.authors="AIZAWA Hina <hina@fetus.jp>"

ENV LANG=C.UTF-8

COPY docker/rpm-gpg/RPM-GPG-KEY-JP3CKI /etc/pki/rpm-gpg/
COPY docker/jp3cki/jp3cki.repo /etc/yum.repos.d/

# EL10 has no postgresql module to disable, but AppStream ships non-modular
# packages with the same names as PGDG's (e.g. postgresql18-server).
# ("postgresql-private-libs" is kept; Remi's php-pgsql links against it.)
# Exclude them so that the PGDG ones are always installed.
RUN rpm --import /etc/pki/rpm-gpg/RPM-GPG-KEY-JP3CKI && \
    dnf install -y dnf-plugins-core epel-release && \
    dnf config-manager --save --setopt='appstream.excludepkgs=postgresql1*' && \
    dnf install -y \
        https://rpms.remirepo.net/enterprise/remi-release-10.rpm \
        "https://download.postgresql.org/pub/repos/yum/reporpms/EL-10-$(uname -m)/pgdg-redhat-repo-latest.noarch.rpm" \
            && \
    dnf module reset -y php && \
    dnf module enable -y php:remi-8.5 && \
    dnf update -y && \
    dnf install -y \
        ImageMagick \
        composer \
        gcc-c++ \
        git-core \
        glibc-langpack-en \
        gzip \
        h2o \
        jpegoptim \
        make \
        nodejs24 \
        nodejs24-npm \
        optipng \
        patch \
        php-cli \
        php-fpm \
        php-gd \
        php-intl \
        php-mbstring \
        php-opcache \
        php-pdo \
        php-pecl-msgpack \
        php-pecl-zip \
        php-pgsql \
        php-process \
        php-sodium \
        php-xml \
        postgresql18 \
        postgresql18-server \
        supervisor \
        unzip \
            && \
    dnf clean all && \
    for cmd in node npm npx; do ln -s "/usr/bin/${cmd}-24" "/usr/local/bin/${cmd}"; done && \
    useradd statink && \
    chmod 701 /home/statink

COPY --chown=statink:statink . /home/statink/stat.ink
COPY --chown=statink:statink docker/config/debug-ips.php /home/statink/stat.ink/config/

USER statink
WORKDIR /home/statink/stat.ink
RUN make clean && \
    make init && \
    ./yii license/extract --interactive=0 && \
    rm -rf ~/.cache ~/.npm

USER postgres
RUN /usr/pgsql-18/bin/initdb \
        --pgdata=/var/lib/pgsql/18/data \
        --encoding=UTF8 \
        --locale=en_US.UTF-8 \
        --auth-local=peer \
        --auth-host=scram-sha-256
COPY --chown=postgres:postgres docker/database/pg_hba.conf /var/lib/pgsql/18/data/pg_hba.conf
COPY --chown=postgres:postgres docker/database/password.php /var/lib/pgsql/

USER root
RUN install -d -o postgres -g postgres -m 2775 /run/postgresql && \
    runuser -u postgres -- /usr/pgsql-18/bin/pg_ctl start -D /var/lib/pgsql/18/data -s -w && \
    runuser -u postgres -- /usr/pgsql-18/bin/createuser --no-createdb --no-createrole --no-superuser statink && \
    runuser -u postgres -- /usr/pgsql-18/bin/createdb --encoding=UTF8 --owner=statink --template=template0 statink && \
    runuser -u postgres -- php /var/lib/pgsql/password.php /home/statink/stat.ink/config/db.php && \
    runuser -l statink -c 'cd ~/stat.ink && make migrate-db && rm -rf runtime/logs' && \
    runuser -u postgres -- /usr/pgsql-18/bin/pg_ctl stop -D /var/lib/pgsql/18/data -s -m fast

COPY docker/php/php.d/ /etc/php.d/
COPY docker/php/php-fpm.d/ /etc/php-fpm.d/
COPY docker/h2o/h2o.conf /etc/h2o/h2o.conf
COPY docker/supervisor/ /etc/supervisord.d/
COPY docker/entrypoint.sh /usr/local/bin/statink-entrypoint

WORKDIR /
EXPOSE 80
CMD ["/usr/local/bin/statink-entrypoint"]
