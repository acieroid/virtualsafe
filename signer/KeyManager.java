import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.ByteArrayInputStream;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.FileWriter;
import java.io.FileReader;
import java.io.StringReader;
import java.io.Reader;
import java.io.DataInputStream;
import java.io.IOException;
import java.io.File;
import java.io.RandomAccessFile;
import java.security.cert.Certificate;
import java.security.cert.CertificateFactory;
import java.security.cert.CertificateException;
import java.security.KeyPair;
import java.security.PrivateKey;
import java.security.Security;
import java.security.Signature;
import org.bouncycastle.jce.provider.BouncyCastleProvider;
import org.bouncycastle.openssl.PEMReader;

/**
 * Class that manage the keys and operations with those keys (signing,
 * verifying, decrypting)
 */
public class KeyManager {
    private static final String CERTIFICATE_FILE = "certificate.crt";
    private static final String CERTIFICATE_KEY_FILE = "certificate.pem";
    private static final String DECRYPTION_KEY_FILE = "key.pem";
    private static final String SIGNATURE_METHOD = "SHA1withRSA";
    /** The directory where the keys are located */
    private String dir;
    /** The certificate of the user */
    private Certificate cert;
    /** The private keys of the user */
    private PrivateKey certKey, decKey;

    /**
     * Create a new key manager, that stores and read keys from the
     * directory @param dir
     */
    public KeyManager(String dir) {
        this.dir = dir;
        /* Create the directory if it does not exists */
        File directory = new File(dir);
        if (!directory.exists()) {
            if (!directory.mkdir()) {
                System.out.println("ERROR: cannot create the directory " + dir);
            }
        }
        /* We need BouncyCastle to read keys from PEM files */
        Security.addProvider(new BouncyCastleProvider());
    }

    /**
     * Check if the keys of the user already exists and loads them.
     * @return true if the keys exists, else false.
     */
    public boolean keysExists() {
        File certFile = new File(dir + "/" + CERTIFICATE_FILE);
        File certKeyFile = new File(dir + "/" + CERTIFICATE_KEY_FILE);
        File decKeyFile = new File(dir + "/" + DECRYPTION_KEY_FILE);

        if (!certFile.exists() || !certKeyFile.exists() || !decKeyFile.exists()) {
            System.out.println("No keys (or not all the required keys) were found");
            return false;
        }
    
        try {
            /* Load the certificate */
            FileInputStream stream = new FileInputStream(certFile);
            cert = parseCertificate(stream);
            if (cert == null) {
                System.out.println("ERROR: cannot read the certificate");
                return false;
            }

            /* Load the certificate private key */
            FileReader reader = new FileReader(certKeyFile);
            certKey = parsePrivateKey(reader);
            if (certKey == null) {
                System.out.println("ERROR: cannot read the certificate key");
                return false;
            }

            /* Load the decryption key */
            reader = new FileReader(decKeyFile);
            decKey = parsePrivateKey(reader);
            if (decKey == null) {
                System.out.println("ERROR: cannot read the decryption key");
                return false;
            }

            System.out.println("All the keys were successfully loaded");
        } catch (Exception e) {
            System.out.println("ERROR: failed to load the keys and certificate: " + e.getMessage());
            return false;
        }
        return true;
    }

    /**
     * Parse the description of the certificate and encryption keys
     * from @param stream and saves them to the user directory.
     * @return true on success, else false.
     */
    public boolean parse(InputStream stream) {
        try {
            BufferedReader reader = new BufferedReader(new InputStreamReader(System.in));
            /* Parse the certificate */
            String certStr = read(reader);
            cert = parseCertificate(new ByteArrayInputStream(certStr.getBytes("US-ASCII")));
            if (cert == null) {
                System.out.println("ERROR: cannot read the certificate");
                return false;
            }

            /* Parse the certificate private key */
            String certPrivKeyStr = read(reader);
            certKey = parsePrivateKey(new StringReader(certPrivKeyStr));
            if (certKey == null) {
                System.out.println("ERROR: cannot read the certificate private key");
                return false;
            }

            /* Parse the decryption private key */
            String decKeyStr = read(reader);
            decKey = parsePrivateKey(new StringReader(decKeyStr));
            if (decKey == null) {
                System.out.println("ERROR: cannot read the decryption key");
                return false;
            }

            System.out.println("Key successfully read.");

            /* Write the keys to the user directory */
            write(certStr, CERTIFICATE_FILE);
            write(certPrivKeyStr, CERTIFICATE_KEY_FILE);
            write(decKeyStr, DECRYPTION_KEY_FILE);
        } catch (Exception e) {
            System.out.println("ERROR: cannot parse certificate or key: " + e.getMessage());
            e.printStackTrace();
            return false;
        }
        return true;
    }

    /**
     * Parse a certificate from a stream
     */
    private Certificate parseCertificate(InputStream stream) throws CertificateException {
        CertificateFactory cf = CertificateFactory.getInstance("X.509");
        return cf.generateCertificate(stream);
    }

    /**
     * Parse a private key from a reader
     */
    private PrivateKey parsePrivateKey(Reader reader) throws IOException {
        return (PrivateKey) new PEMReader(reader).readObject();
    }

    /**
     * Read a something from the console input until two \n are found
     */
    private String read(BufferedReader reader) throws IOException {
        StringBuilder sb = new StringBuilder();

        String pkey;
        int c;
        char prev = '\0';
        while((c = reader.read()) != -1) {
            char character = (char) c;
            if (character == '\n' && prev == '\n') {
                /* end of the key */
                return sb.toString();
            }
            sb.append(character);
            prev = character;
        }
        return sb.toString();
    }

    /**
     * Write something to a file in the user directory
     */
    private void write(String content, String file) throws IOException {
        BufferedWriter out = new BufferedWriter(new FileWriter(dir + "/" + file));
        out.write(content);
        out.close();
    }

    /**
     * Sign the file @param file and store the resulting signature
     * to @param signature.
     * @return true on success, else false.
     */
    public boolean sign(String file, String signature) {
        try {
            /* Read the file content */
            byte[] data = fileToByteArray(file);

            /* Sign the data */
            Signature signer = Signature.getInstance(SIGNATURE_METHOD);
            signer.initSign(certKey);
            signer.update(data);
            byte[] signedData = signer.sign();

            /* Write the signature */
            byteArrayToFile(signedData, signature);
        } catch (Exception e) {
            System.out.println("ERROR: cannot sign the file: " + e.getMessage());
            return false;
        }
        return true;
    }

    /**
     * Read the content of a file and return it as a byte array
     */
    private byte[] fileToByteArray(String file) throws IOException {
        RandomAccessFile f = new RandomAccessFile(file, "r");
        byte[] b = new byte[(int)f.length()];
        f.read(b);
        return b;
    }

    /**
     * Write the content of a byte array into a file
     */
    private void byteArrayToFile(byte[] array, String file) throws IOException {
        FileOutputStream out = new FileOutputStream(file);
        out.write(array);
        out.close();
    }

    /**
     * Check that the file @param file match its signature, stored
     * at @param signature.
     * @return true on success, else false.
     */
    public boolean check(String file, String signature) {
        return false; /* TODO */
    }

    /**
     * Decrypt the file @param fileIn with the key stored (encrypted)
     * in @param keyFile, and save the decrypted file to @param
     * fileOut.
     * @return true on success, else false.
     */
    public boolean decrypt(String fileIn, String fileOut, String keyFile) {
        return false; /* TODO */
    }
}
