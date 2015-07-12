import subprocess
subp=subprocess.Popen('./test.sh',shell=True,stdout=subprocess.PIPE)
while subp.poll()==None:
    print subp.stdout.readline()
print subp.returncode

