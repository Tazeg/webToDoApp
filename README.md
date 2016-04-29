# Web ToDo app

A simple ToDo application.
It's running on your web site, or local network on a Raspberry Pi.
A docker image is available.

![Web ToDo app by JeffProd](http://www.jeffprod.com/github/webtodoapp.png)

### Features

- quick add task
- add tags, note, due date, deadline, priority
- quick button : "Do it tomorrow"
- tabs : today, tomorrow, scheduled, next, tags
- search in title and note
- supported languages : fr, en

### Installation

##### Docker

Simply pull image from Docker Hub :
```
docker pull jeffprod/webtodoapp
```

Create a local directory on your computer to save todos (SQLite database):
```
mkdir -p /home/[user]/data
```

Start image for the first time :
```
docker run --name=webtodoapp -d -p 80:80 -v /home/user/data:/data jeffprod/webtodoapp
```

Open browser and enjoy :
http://localhost

To stop the docker container :
```
docker stop webtodoapp
```

##### Apache/PHP

If you already a web server, just copy the `www` directory in your web site.

### Credits :

- http://getbootstrap.com/
- http://fontawesome.io/
- http://silviomoreto.github.io/bootstrap-select/
- http://jquery.com/
- https://github.com/scottjehl/Respond
- https://github.com/eternicode/bootstrap-datepicker