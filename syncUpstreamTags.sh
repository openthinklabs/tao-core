#!/bin/bash

#git remote add upstream https://github.com/oat-sa/tao-core.git
git fetch --tags upstream
git push -f --tags origin master

