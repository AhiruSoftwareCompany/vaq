BEGIN {
    FS = "\t+"
    if (i+1 == 1) {print "You need to specify a starting id"; exit} # Check if i is set
}
$0 != "" {
    print "id: " i "\ndate: " $2 "\n\n" $1 ": " $3 "\n---"
    i++
}
