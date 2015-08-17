import smtplib
import datetime
import sys
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText


receiver= "Ben.liu@agrf.org.au"

def send_email(recipients, subject, content):
    """
    Send email
    """
    try:
        smtp = smtplib.SMTP('localhost')
        message="Subject: %s\n\n%s" % (subject, content)
        smtp.sendmail("blast@spectacle.agrf.org.au", recipients, message)
        smtp.quit()
        return True
    except smtplib.SMTPException as e:
        logging.error("Problem sending email with subject %s: %s" % (subject, e))
        return False


#def send_html_email(recipients, subject, content):
def send_html_email():
    msg = MIMEMultipart('alternative')
    msg['Subject'] = "Testing"
    msg['From'] = "ben.liu@agrf.org.au"
    msg['To'] = "ben.liu@agrf.org.au" 


    #text = "Hi!\nHow are you?\nHere is the link you wanted:\nhttp://www.python.org"
    html = """\
        <html>
        <head></head>
        <body>
        <p>Hi!<br>
            Blast finished<br>
            Here is the <a href="http://www.python.org">link</a> you wanted.
        </p>
        </body>
        </html>
        """
    #part1 = MIMEText(text, 'plain')
    part2 = MIMEText(html, 'html')
    
    #msg.attach(part1)
    msg.attach(part2)
    
    s = smtplib.SMTP('localhost')
    # sendmail function takes 3 arguments: sender's address, recipient's address
    # and message to send - here it is sent as one string.
    s.sendmail(msg['From'], msg['To'], msg.as_string())
    s.quit()



if(__name__=="__main__"):
    send_email(receiver, str(sys.argv[1])+" is finished", "")    
    #send_html_email()


