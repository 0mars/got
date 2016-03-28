server {
    listen  80;

    root {{ nginx.docroot }};
    index app_dev.php;

    server_name {{ nginx.servername }};

    location / {
        sendfile off;
        try_files $uri /app_dev.php$is_args$args;
    }


    error_page 404 /404.html;

    error_page 500 502 503 504 /50x.html;
        location = /50x.html {
        root /usr/share/nginx/www;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

