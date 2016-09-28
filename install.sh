#!/usr/bin/env bash

rm -f data/entities/*.xml && ./joomla install \
    extensions/Article \
    extensions/Workflow \
    libraries/incubator/Media \
    libraries/incubator/PageBuilder
