#!/usr/bin/env python

import ftputil
import os

currentPath=os.path.dirname(os.path.realpath(__file__))

with ftputil.FTPHost('ftp.ensembl.org', 'anonymous', '') as host:
    host.chdir("/pub/release-76/fasta/")
    ary_names = host.listdir(host.curdir)

    for species in ary_names:
        host.chdir("/pub/release-76/fasta/"+species+"/dna/")
        ary_files= host.listdir(host.curdir)

        for file in ary_files:
            if("_sm" not in file and "_rm" not in file):
                print "Downloading ...."
                print file
                host.download(file,currentPath+"/"+file)
