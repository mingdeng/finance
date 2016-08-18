#encoding=utf-8
import MySQLdb as mdb
import ystockquote

def stockquote(stock):
        data = ystockquote.get_historical_prices(stock, '2016-08-16','2016-08-17')
        data = data.items()
        data.sort()
        close = data[0][1]['Close']
        vol = data[0][1]['Volume']
        num = 0
        short = 0
        datedata = [] 
        for key,value in data:
                if float(value['Close']) < float(close) and int(value['Volume']) > int(vol):
                        num = num + 1
                else:
                        num =0
                datedata.append([num,key,value['Close'],value['Volume']])
                close = value['Close']
                vol = value['Volume']
        if num >= 1:
                for v in datedata:
                        print v[0],v[1],v[2],v[3]  
                
con = None
con = mdb.connect('127.0.0.1', 'root', 'sp6161266', 'short_stocks', charset='utf8')
cur = con.cursor()
cur.execute("select * from stock")
rows = cur.fetchall()
for row in rows:
    print row[1],row[2],row[3],'chg',row[5]
    stockquote(row[3])
    
