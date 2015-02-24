#!/bin/bash
cd "$(dirname "$0")"
git reset --hard HEAD;
git stash;
git pull;
composer update;
