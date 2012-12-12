import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.BufferedReader;
import java.io.StringReader;
import java.io.DataInputStream;
import java.io.IOException;
import java.security.cert.Certificate;
import java.security.cert.CertificateFactory;
import java.security.cert.CertificateException;
import java.security.KeyPair;
import java.security.PrivateKey;
import java.security.Security;
import org.bouncycastle.jce.provider.BouncyCastleProvider;
import org.bouncycastle.openssl.PEMReader;

/**
 * Class that manage the keys and operations with those keys (signing,
 * verifying, decrypting)
 */
public class KeyManager {
    /** The directory where the keys are located */
    private String dir;

    /**
     * Create a new key manager, that stores and read keys from the
     * directory @param dir
     */
    public KeyManager(String dir) {
        this.dir = dir;
    }

    /**
     * Check if the keys of the user already exists.
     * @return true if the keys exists, else false.
     */
    public boolean keysExists() {
        return false; /* TODO */
    }

    /**
     * Parse the description of the certificate and encryption keys
     * from @param stream and saves them to the user directory.
     * @return true on success, else false.
     */
    public boolean parse(InputStream stream) {
        try {
            DataInputStream in = new DataInputStream(System.in);
            /* Parse the certificate */
            CertificateFactory cf = CertificateFactory.getInstance("X.509");
            Certificate cert = cf.generateCertificate(stream);

            in.readByte(); /* drop the \n between the certificate and the next key*/

            BufferedReader reader = new BufferedReader(new InputStreamReader(System.in));
            /* Parse the certificate private key */
            StringReader sr = new StringReader(readPrivKey(reader));
            Security.addProvider(new BouncyCastleProvider());
            PrivateKey certPrivKey = (PrivateKey) new PEMReader(sr).readObject();
            if (certPrivKey == null) {
                System.out.println("ERROR: cannot read the certificate private key");
                return false;
            }

            /* Parse the decryption private key */
            sr = new StringReader(readPrivKey(reader));
            PrivateKey decKey = (PrivateKey) new PEMReader(sr).readObject();
            if (decKey == null) {
                System.out.println("ERROR: cannot read the decryption key");
                return false;
            }

            System.out.println("Key successfully read.");

        } catch (Exception e) {
            System.out.println("ERROR: cannot parse certificate: " + e.getMessage());
            e.printStackTrace();
            return false;
        }
        return false;
    }

    /**
     * Read a private key from the console input
     */
    private String readPrivKey(BufferedReader reader) throws IOException {
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
     * Sign the file @param file and store the resulting signature
     * to @param signature.
     * @return true on success, else false.
     */
    public boolean sign(String file, String signature) {
        return false; /* TODO */
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
