# Disqus export [depracated: no easy way to get user emails]

Create a json export file with all comments in a specified forum. To get started copy the `default.config.yml` file to `config.yml` and add your api key and the forum you want to export. Note that the disqus API is rate limited to 1000 calls per hour, so if you have more than ~950 threads this script will have to be modified.