FROM phpdockerio/php:8.2-fpm
WORKDIR "/app"

RUN curl -sL https://deb.nodesource.com/setup_18.x | bash;
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -; \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list;

RUN apt-get update; \
    apt-get -y --no-install-recommends install \
      php8.2-intl \
      php8.2-mysql \
      gcc g++ make \
      nodejs \
      yarn; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*;

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
