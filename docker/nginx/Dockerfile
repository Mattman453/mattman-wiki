FROM nginx:alpine

RUN apk add --no-cache \
    augeas \
    gcc \
    nano \
    python3 \
    python3-dev \
    sudo

RUN python3 -m venv /opt/certbot && \
    /opt/certbot/bin/pip install --upgrade pip

RUN /opt/certbot/bin/pip install certbot certbot-nginx

RUN echo "0 0,12 * * * root /opt/certbot/bin/python -c 'import random; import time; time.sleep(random.random() * 3600)' && sudo certbot renew -q" | \
    sudo tee -a /etc/crontab > /dev/null
