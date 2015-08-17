# convert A <=> T, C <=> G
# 
# 2013 @ AGRF

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


def rev(seq):
    return seq[::-1]


def comp(seq):
    return convert(seq)


def revComp(seq):
    return convert(seq[::-1])
