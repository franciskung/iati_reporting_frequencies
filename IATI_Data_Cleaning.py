# -*- coding: utf-8 -*-
# <nbformat>3.0</nbformat>

# <codecell>

import matplotlib.pyplot as plt
from datetime import datetime
import pandas
import json
import pylab

# <codecell>

with open('output.json', 'r') as content_file:
    content = content_file.read()

# <codecell>

raw_data = json.loads(content)

# <codecell>

for activity, values in raw_data.iteritems():
    dates = []
    for revision in values['update_dates']:
        date = datetime.strptime(revision['timestamp'][0:10], "%Y-%m-%d")
        revision['timestamp'] = date
        dates.append(date)
        
    new_dates = list(set(dates))
    new_dates.sort()
    string_dates = []
    for d in new_dates:
        string_dates.append(d.strftime("%Y-%m-%d"))
    values['update_dates'] = string_dates
    
    activity_count = int(values['activity_count'].replace('"', '') or "0")
    increment = activity_count / len(new_dates)
    level = 0
    values['activity_increments'] = []
    for d in values['update_dates']:
        level = level + increment
        values['activity_increments'].append(level)
    #round up the last value so that it matches the total
    values['activity_increments'][len(values['activity_increments']) - 1] = activity_count
        
    values['update_data'] = zip(values['update_dates'], values['activity_increments'])
    del(values['update_dates'])
    del(values['activity_increments'])
        

# <codecell>

raw_data

# <codecell>

f = open('output_clean.json', 'w')
f.write(json.dumps(raw_data, sort_keys=True, indent=2))
f.close()

# <codecell>


