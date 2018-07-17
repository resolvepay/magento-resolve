#!/bin/bash

mkdir -p var/build

#OS X, prevent ._ files
export COPYFILE_DISABLE=true

tar -cvf var/build/Resolve_Resolve.tar app/code/community/Resolve/Resolve app/etc/modules/Resolve_Resolve.xml app/design/frontend/base/default/template/resolve app/design/frontend/base/default/layout/resolve app/design/frontend/base/default/layout/resolve skin/frontend/base/default/js/resolve