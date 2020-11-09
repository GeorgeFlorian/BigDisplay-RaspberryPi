
filename=Metrici_MP_Display.zip
n_bytes=950
out_file=hash_code.txt
head -c $n_bytes $filename | md5sum  > $out_file
