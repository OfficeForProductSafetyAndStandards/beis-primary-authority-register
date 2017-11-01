array=( ASD DAS OIS )
for i in "${array[@]}"
do
	export $i="$i_var"
done

echo $ASD
echo $DAS
echo $OIS

