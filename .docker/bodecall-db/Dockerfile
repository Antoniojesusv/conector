FROM mysql:5.7

# Setup the custom configuration
ADD conf.d/mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf

RUN sed -i "s/#log-error/log-error/g" /etc/mysql/mysql.conf.d/mysqld.cnf
