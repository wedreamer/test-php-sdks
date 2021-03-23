#!/bin/bash

# Set the textdomain for the translations using $"..."
TEXTDOMAIN="apt"

# Get the configuration from /etc/apt/apt.conf
CLEAN="prompt"
OPTS=""
DSELECT_UPGRADE_OPTS="-f"
APTGET="/usr/bin/apt-get"
DPKG="/usr/bin/dpkg"
DPKG_OPTS="--admindir=$1"
APT_OPT0="-oDir::State::status=$1/status"
APT_OPT1="-oDPkg::Op