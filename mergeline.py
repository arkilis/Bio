#!/usr/bin/python

# This script is used to merge multiple lines into one line in fasta file

import os
import sys


def merge(path):
    if(os.path.exists(path)==False):
        print "Not found path: "+path
        help()
        sys.exit(1) 

    fp_old= open(path)
    fp_new= open(path+".new", "w")

    for line in fp_old:
        if(line[0]==">"):
            fp_new.write(line)
        else:
            line=line.replace('\n','')
            fp_new.write(line)
    
    fp_old.close()
    fp_new.close()
            

# help function
def help():
    print "Usage: python mergeline.py test.fasta "
    

if(__name__=="__main__"):
    if(len(sys.argv)!=2):
        help()
    else:
        merge(sys.argv[1])    


