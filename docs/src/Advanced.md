### Advanced

1. *Which template engine helium is using?*

`Helium` uses the symfony default templating engine `Twig`. Here is a guide how to use [twig](https://twig.symfony.com/doc/3.x/templates.html)


2. *How to build a new home page template?*

By default helium scans the `templates/default/page/home.$layout.html.twig`. To create a new layout called creative. create a new file `templates/default/page/home.creative.html.twig` from `templates/default/page/home.default.html.twig`. then customize the look and feel.

You can activate the new layout from the dashboard > settings page, select the `creative` layout.


3. *How to build a new newsletter email template?*

Let's create a new newsletter template for product updates. First we create `templates/default/newsletter/product_updates.distro.html.twig` and `templates/default/newsletter/product_updates.distro.yml`.

By default the following variables are available to be used in your newsletter template

- `app_name`
- `app_url`
- `data.unsubscribe_url`

You can define some dynamic data in `templates/default/newsletter/product_updates.distro.yml`

```
logo_url: 'https://via.placeholder.com/360x170'

updates:
  -
    title: How to Setup a HA Cassandra Cluster
    img_url: 'https://via.placeholder.com/1200x700'
    link: 'https://clivern.com/how-to-setup-a-ha-cassandra-cluster/'
    description: >-
      Apache Cassandra is a NoSQL database with flexible deployment options that’s
      highly performant (especially for writes), scalable, fault-tolerant, and
      proven in production. Alternative NoSQL databases include Amazon DynamoDB,
      Apache HBase, and MongoDB.
  -
    title: How to Setup a HA Cassandra Cluster
    img_url: 'https://via.placeholder.com/1200x700'
    link: 'https://clivern.com/how-to-setup-a-ha-cassandra-cluster/'
    description: >-
      Apache Cassandra is a NoSQL database with flexible deployment options that’s
      highly performant (especially for writes), scalable, fault-tolerant, and
      proven in production. Alternative NoSQL databases include Amazon DynamoDB,
      Apache HBase, and MongoDB.

  -
    title: How to Setup a HA Cassandra Cluster
    img_url: 'https://via.placeholder.com/1200x700'
    link: 'https://clivern.com/how-to-setup-a-ha-cassandra-cluster/'
    description: >-
      Apache Cassandra is a NoSQL database with flexible deployment options that’s
      highly performant (especially for writes), scalable, fault-tolerant, and
      proven in production. Alternative NoSQL databases include Amazon DynamoDB,
      Apache HBase, and MongoDB.
```

You can access the above data from the template using `data.logo_url` and `data.updates` like the following

```
{% for update in data.updates %}
    <img src="{{ update.img_url }}">
    <table>
        <tbody>
            <tr>
                <td >
                    <h1>{{ update.title }}</h1>
                    <p>{{ update.description }}</p>
                    <a href="{{ update.link }}">
                        <span>Learn more</span>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
{% endfor %}
```

*Please note that all variables in the `product_updates.distro.yml` can be accessed by data.$$*