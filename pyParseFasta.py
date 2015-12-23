#!/usr/bin/python
from pprint import pprint

# parse text to fasta objects and store in hash table
class Seq(object):
    def __init__(self,key,records_dict):
        self.key = key
        self.records = records_dict

    def tostring(self):
        s = self.records[self.key]['sequence']
        return s.replace('\n','')


class SeqRecord(object):
    def __init__(self,key,records_dict):
        self.records = records_dict
        self.key = key
        self.seq = Seq(key,records_dict)

    def format(self,out_format):
        if out_format == 'fasta':
            r = self.records[self.key]
            return ">%s\n%s" % (r['description'],r['sequence'])

    def desOutput(self):
            return "%s" % self.records[self.key]['description']


    def seqOutput(self):
            return "%s" % self.records[self.key]['sequence']

class FastaParser(object):
    def __init__(self,fasta_file):
        self.fasta_file = fasta_file
        fasta = open(fasta_file,'r').read()
        self.entries = [x for x in fasta.split('>') if len(x) != 0]
        self.build_records_dict()

    def keys(self):
        keys_list = []
        for entry in self.entries:
            key = [x for x in entry.split('\n')[0].split() if len(x) != 0][0]
            keys_list.append(key)
        return [x.strip() for x in keys_list]

    def __len__(self):
        return len(self.keys())

    def __iter__(self):
        for k in self.keys():
            yield k

    def build_records_dict(self):
        records_dict = {}
        for entry in self.entries:
            key = [x for x in entry.split('\n')[0].split() if len(x) != 0][0]
            description = entry.split('\n')[0]
            sequence = '\n'.join(entry.split('\n')[1:]).strip()
            records_dict[key] = {'description':description,'sequence':sequence}
        self.records = records_dict

    def __getitem__(self,key):
        return SeqRecord(key,self.records)



qv      = 30
path    = "/home/liub/data/11623/sent_to_client/trimmed_"

def execute(fa, qual, name):
    for k1,k2 in zip(fa.keys(), qual.keys()):
        seqRes  = []
        qulRes  = []

        szQual  = qual[k2].seqOutput().replace("\r\n","")
        aryQual = szQual.split(" ")
        index   = 0
        for q in aryQual:
            if(int(q)>=qv):
                seqRes.append(fa[k1].seqOutput()[index])
                qulRes.append(aryQual[index])
            index+=1

        # create fasta file
        with open(path+name+"/"+fa[k1].desOutput()[:20]+"_trimmed.fasta", 'wb') as fp:
            fp.write(">"+fa[k1].desOutput()[:20]+"\n")
            fp.write("".join(seqRes))
            fp.close()

        with open(path+name+"/"+fa[k1].desOutput()[:20]+"_trimmed.qual", 'wb') as fp:
            fp.write(">"+fa[k1].desOutput()[:20]+"\n")
            fp.write(" ".join(qulRes))
            fp.close()

if(__name__=="__main__"):



    name    = [
               "tr12_ak2",
               "tr12_bi2",
               "tr12_cf7",
               "tr12_ch2",
               "tr12_cj4",
               "tr12_cn3",
                ]



    for n in name:
        fa      = FastaParser(path+n+"/"+n+"_manual.fasta")
        qual    = FastaParser(path+n+"/"+n+"_manual.qual")
        execute(fa, qual, n)
