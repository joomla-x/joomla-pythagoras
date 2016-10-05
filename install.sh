#!/usr/bin/env bash

rm -f data/entities/*.xml \
  && rm -f config/extensions.ini \
  && ./joomla install -vvv \
    extensions/Article \
    libraries/incubator/Media \
    libraries/incubator/PageBuilder
