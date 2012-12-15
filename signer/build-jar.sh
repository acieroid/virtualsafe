#!/bin/sh
BCPROVJAR="/usr/share/java/bcprov.jar"
BUILDDIR="build/"

mkdir -p "$BUILDDIR"

echo "Copy BouncyCastle files"
unzip "$BCPROVJAR" -d "$BUILDDIR"

echo "Compiling application"
javac -cp "$BCPROVJAR" *.java
cp *.class "$BUILDDIR"

echo "Adding manifest"
mkdir -p "$BUILDDIR/META-INF"
cat > "$BUILDDIR/META-INF/MANIFEST.MF" <<EOF
Main-Class: Signer
EOF

echo "Creating JAR"
cd "$BUILDDIR"
zip signer.jar `find . -type f`
cd -
mv "$BUILDDIR/signer.jar" ./

echo "Removing build directory"
rm -r "$BUILDDIR"

echo "Done."
