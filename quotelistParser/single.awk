BEGIN {
    FS = "\t+"
    if (start+1 == 1) {print "You need to specify a starting id"; exit} # Check if start is set
}
$0 != "" {
    print "id: " start "\ndate: " $2 "\norigin: " origin "\n\n" $1 ": " $3 "\n---"
    i++
}
