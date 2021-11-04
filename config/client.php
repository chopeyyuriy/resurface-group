<?php

return [
    'status' => [
        1 => 'Active', 'Draft', 'Archived'
    ],

    'relationship' => [
        1 => 'Identified Patient', 'Husband', 'Wife', 'Son', 'Daughter', 'Father', 'Mother', 'Other',
    ],

    'marital_status' => [
        1 => 'Single', 'Married', 'Divorced', 'Domestic Partnership', 'Long-Term Relationship', 'Widowed',
    ],

    'gender' => [
        'f' => 'Female', 
        'm' => 'Male', 
        'n' => 'Non-Binary', 
        't' => 'Transgender', 
        'o' => 'Other', 
        'i' => 'Intersex'
    ],

    'race' => [
        1 => 'American Indian or Alaska Native', 
             'Asian', 
             'Black or African American', 
             'Hispanic or Latinx', 
             'Middle Eastern or North African', 
             'Native Hawaiian or Other Pacific Islander', 
             'White', 
             'Other',
    ],
    
    'phone' => [
        1 => 'Office', 'Mobile', 'Home', 'Other'
    ]
];
