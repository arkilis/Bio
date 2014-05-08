# convert 

def convert(seq):
    sz=""
    if(len(seq)!=0):
        for char in seq:
            if(char=="A"):
                sz+="T"
            if(char=="T"):
                sz+="A"
            if(char=="C"):
                sz+="G"
            if(char=="G"):
                sz+="C"
        return sz


sz="TGGCTAACAAGTCAGCAGAGG"
# show original
print sz
# show complimentary
print convert(sz)
# show complimentary reverse
print convert(sz)[::-1]
