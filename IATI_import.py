# -*- coding: utf-8 -*-
# <nbformat>3.0</nbformat>
# This is the IPython notebook script used to fetch the IATI data from the IATI CKAN Portal
# <codecell>

import os

# <codecell>

import ckanapi

# <codecell>

import json

# <codecell>

iati = ckanapi.RemoteCKAN('http://www.iatiregistry.org/')

# <codecell>

publishers = iati.action.group_list()

# <codecell>

packages = iati.action.package_list()

# <codecell>

dump = {}
i = 0
for package_id in packages:
    print 'importing package #%s : %s' % (i, package['title'])
    i = i+1
    package = iati.action.package_show(id = package_id)
    try:
        publisher_id = (item for item in package['extras'] if item['key'] == 'publisher_iati_id').next()['value']
        country = (item for item in package['extras'] if item['key'] == 'country').next()['value']
    except StopIteration:
        publisher_id = publisher_id or ''
        country = country or ''
    dump[package_id] = {'name': package['title'], 'publisher': publisher_id, 'country': country, 'update_dates': []}
    revisions = iati.action.package_revision_list(id = package_id)
    for revision in revisions:
        dump[package_id]['update_dates'].append(revision['timestamp'])

# <codecell>

dump

# <codecell>

f = open('output.json', 'w')
f.write(json.dumps(dump, sort_keys=True, indent=2))
f.close()

# <codecell>


