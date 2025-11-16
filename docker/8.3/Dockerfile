FROM ubuntu:24.04

LABEL maintainer="Taylor Otwell"

ARG WWWGROUP
ARG NODE_VERSION=22
ARG MYSQL_CLIENT="mysql-client"
ARG POSTGRES_VERSION=16

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV SUPERVISOR_PHP_COMMAND="/usr/bin/php -d variables_order=EGPCS /var/www/html/artisan serve --host=0.0.0.0 --port=80"
ENV SUPERVISOR_PHP_USER="sail"
ENV PLAYWRIGHT_BROWSERS_PATH=0

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN echo "Acquire::http::Pipeline-Depth 0;" > /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::http::No-Cache true;" >> /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::BrokenProxy    true;" >> /etc/apt/apt.conf.d/99custom

# ================== PASO 1: Configurar repositorios base ==================
RUN sed -i 's|archive.ubuntu.com|mirror.us.leaseweb.net|g' /etc/apt/sources.list && \
    apt-get update -y && \
    DEBIAN_FRONTEND=noninteractive apt-get upgrade -y

# ================== PASO 2: Instalar dependencias del sistema ==================
RUN mkdir -p /etc/apt/keyrings && \
    apt-get install -y --no-install-recommends \
      gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 \
      libcap2-bin libpng-dev python3 dnsutils librsvg2-bin fswatch ffmpeg nano

# ================== PASO 3: Configurar repositorio PHP 8.3 ==================
RUN curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0xb8dc7e53946656efbce4c1dd71daeaab4ad4cab6' | \
    gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null && \
    echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu noble main" > \
    /etc/apt/sources.list.d/ppa_ondrej_php.list && \
    apt-get update -y

# ================== PASO 4: Instalar PHP 8.3 y extensiones ==================
RUN apt-get install -y --no-install-recommends \
       php8.3-cli php8.3-dev php8.3-pgsql php8.3-sqlite3 php8.3-gd \
       php8.3-curl php8.3-imap php8.3-mysql php8.3-mbstring php8.3-xml \
       php8.3-zip php8.3-bcmath php8.3-soap php8.3-intl php8.3-readline \
       php8.3-ldap php8.3-msgpack php8.3-igbinary php8.3-redis \
       php8.3-memcached php8.3-pcov php8.3-xdebug

# ================== PASO 5: Instalar extensiones PHP adicionales (opcionales) ==================
RUN apt-get install -y --no-install-recommends \
       php8.3-imagick php8.3-swoole php8.3-mongodb || true

# ================== PASO 6: Instalar Composer ==================
RUN curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# ================== PASO 7: Configurar Node.js ==================
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | \
    gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" > \
    /etc/apt/sources.list.d/nodesource.list && \
    apt-get update -y

# ================== PASO 8: Instalar Node.js y herramientas ==================
RUN apt-get install -y --no-install-recommends nodejs yarn postgresql-client-$POSTGRES_VERSION $MYSQL_CLIENT

# ================== PASO 9: Instalar gestores de paquetes Node ==================
RUN npm install -g npm@latest pnpm bun

# ================== PASO 10: Instalar Playwright (opcional) ==================
RUN npx playwright install-deps || true

# ================== PASO 11: Limpiar caché y archivos temporales ==================
RUN apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# ================== Configuración de permisos PHP ==================
RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.3

# ================== Crear usuario sail ==================
RUN userdel -r ubuntu && \
    groupadd --force -g $WWWGROUP sail && \
    useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

RUN git config --global --add safe.directory /var/www/html

# ================== Copiar archivos de configuración ==================
COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.3/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

# === Fix: composer home y permisos para usuario sail ===
ENV HOME=/home/sail
RUN mkdir -p /home/sail/.composer \
    && chown -R sail:sail /home/sail \
    && chmod -R 775 /home/sail

EXPOSE 80/tcp

ENTRYPOINT ["start-container"]