# Compare two GitHub repositories app

This is simple app to compare two GitHub repositories. You can easily compare open source
libraries and chose better option for your project!

# How to run project

##### Requirements  

  - Linux  
  - Composer
  - Git
  - Docker

##### Installation

1. Clone this repository:
```
git clone https://github.com/michalkowalik97/volt.io.git
```
2. Move to downloaded dir, copy **.env.example** and paste as **.env**:
```
cd volt.io
cp .env.example .env
```
3. Open **.env** file and fill **REPO_API_USERNAME**(Your GitHun username) and **REPO_API_TOKEN**(GitHub personal access tokens) - *This step is optional*
4. Run composer install:
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```
5. Add alias to *laravel sail*:
```
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
```
5. Generate app key:
```
sail artisan key:generate
```
6. Run app:
```
sail up
```
