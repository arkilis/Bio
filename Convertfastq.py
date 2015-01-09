from Bio import SeqIO
import os
import sys

# convert a fastq file to responding fasta and qual files
# Ben liu @ AGRF 
# JAN 2014
# 
if(__name__=="__main__"):
    if(len(sys.argv)!=2):
        print "Invalid arguments!"
        sys.exit(1)
    
    filename= os.path.basename(sys.argv[1])[:-6]
    dirname= os.path.dirname(sys.argv[1])
    SeqIO.convert(sys.argv[1], "fastq", filename+".fasta", "fasta") 
    SeqIO.convert(sys.argv[1], "fastq", filename+".qual", "qual") 
