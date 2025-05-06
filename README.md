###

     php artisan parse:proxies --input_file=storage/input/example.txt --output_file=storage/output/proxies.json --format=string
     php artisan parse:proxies --input_file=storage/input/example.txt --output_file=storage/output/proxies.json --format=string --login_filter=Rcs18F
     php artisan parse:proxies --only=ip --input_file=storage/input/ips.txt --output_file=storage/output/proxies.json --format=string --login_filter=Rcs18F