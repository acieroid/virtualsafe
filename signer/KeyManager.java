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
import java.security.AlgorithmParameters;
import org.bouncycastle.jce.provider.BouncyCastleProvider;
import org.bouncycastle.openssl.PEMReader;
import javax.crypto.Cipher;
import javax.crypto.CipherOutputStream;
import javax.crypto.KeyGenerator;
import javax.crypto.SecretKey;
import javax.crypto.SecretKeyFactory;
import javax.crypto.spec.SecretKeySpec;
import javax.crypto.spec.IvParameterSpec;

/**
 * Class that manage the keys and operations with those keys (signing,
 * verifying, decrypting)
 */
public class KeyManager {
    private static final String CERTIFICATE_FILE = "certificate.crt";
    private static final String CERTIFICATE_KEY_FILE = "certificate.pem";
    private static final String ENCRYPTION_KEY_FILE = "key.crt";
    private static final String DECRYPTION_KEY_FILE = "key.pem";
    private static final String SIGNATURE_METHOD = "SHA1withRSA";
    private static final String ASYMMETRIC_ENCRYPTION_METHOD = "RSA";
    private static final String ENCRYPTION_ALGORITHM = "AES";
    private static final String ENCRYPTION_METHOD = "AES/CBC/NoPadding";
    private static final int ENCRYPTION_KEYSIZE = 256;
    /** The directory where the keys are located */
    private String dir;
    /** The certificate and encryption key of the user */
    private Certificate cert = null, encKey = null;
    /** The private keys of the user */
    private PrivateKey certKey = null, decKey = null;

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
        File encKeyFile = new File(dir + "/" + ENCRYPTION_KEY_FILE);
        File decKeyFile = new File(dir + "/" + DECRYPTION_KEY_FILE);

        if (!certFile.exists() || !certKeyFile.exists() || !encKeyFile.exists() || !decKeyFile.exists()) {
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

            /* Load the encryption key */
            stream = new FileInputStream(encKeyFile);
            encKey = parseCertificate(stream);
            if (encKey == null) {
                System.out.println("ERROR: cannot read the encryption key");
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
            BufferedReader reader = new BufferedReader(new InputStreamReader(stream));
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

            /* Parse the encryption key */
            String encKeyStr = read(reader);
            encKey = parseCertificate(new ByteArrayInputStream(encKeyStr.getBytes("US-ASCII")));
            if (encKey == null) {
                System.out.println("ERROR: cannot read the encryption key");
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
            write(encKeyStr, ENCRYPTION_KEY_FILE);
            write(decKeyStr, DECRYPTION_KEY_FILE);
        } catch (Exception e) {
            System.out.println("ERROR: cannot parse certificate or key: " + e.getMessage());
            return false;
        }
        return true;
    }

    /**
     * Parse a new certificate and saves it
     * @return true on success, else false.
     */
    public boolean newCertificate(InputStream stream) {
        try {
            BufferedReader reader = new BufferedReader(new InputStreamReader(stream));
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

            /* Saves the certificate and the key, overwriting the previous values */
            write(certStr, CERTIFICATE_FILE);
            write(certPrivKeyStr, CERTIFICATE_KEY_FILE);
        } catch (Exception e) {
            System.out.println("ERROR: cannot parse the certificate: " + e.getMessage());
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
            byte[] data = fileToBytes(file);

            /* Sign the data */
            Signature signer = Signature.getInstance(SIGNATURE_METHOD);
            signer.initSign(certKey);
            signer.update(data);
            byte[] signedData = signer.sign();

            /* Write the signature in a human-readable format */
            bytesToFile(bytesToHex(signedData).getBytes(), signature);
        } catch (Exception e) {
            System.out.println("ERROR: cannot sign the file: " + e.getMessage());
            return false;
        }
        return true;
    }


    /**
     * Same as three-argument check, but for a file signed by this user
     * @return true on succes, else false
     */
    public boolean check(String file, String signature) {
        return check(file, signature, dir + "/" + CERTIFICATE_FILE);
    }

    /**
     * Check that the file @param file match its signature, stored
     * at @param signature, signed by the owner of @param certificate.
     * @return true on success, else false.
     */
    public boolean check(String file, String signature, String certificate) {
        try {
            /* Read the file content */
            byte[] data = fileToBytes(file);

            /* Read the signature from a human-readable format */
            byte[] signedData = hexToBytes(new String(fileToBytes(signature)));

            /* Read the certificate */
            FileInputStream stream = new FileInputStream(certificate);
            Certificate cert = parseCertificate(stream);
            if (cert == null) {
                System.out.println("ERROR: cannot read the certificate");
                return false;
            }

            /* Check the signature */
            Signature signer = Signature.getInstance(SIGNATURE_METHOD);
            signer.initVerify(cert);
            signer.update(data);
            return signer.verify(signedData);
        } catch (Exception e) {
            System.out.println("ERROR: cannot check the signature: " + e.getMessage());
            return false;
        }
    }

    /**
     * Encrypt the file @param fileIn, storing the result in @param
     * fileOut and the key used, encrypted with the public key,
     * to @param keyFile.
     * @return true on success, else false.
     */
    public boolean encrypt(String fileIn, String fileOut, String keyFile) {
        try {
            /* Generate a random key */
            KeyGenerator kg = KeyGenerator.getInstance(ENCRYPTION_ALGORITHM);
            kg.init(ENCRYPTION_KEYSIZE);
            SecretKey key = kg.generateKey();

            /* Initialize the cipher */
            Cipher cipher = Cipher.getInstance(ENCRYPTION_METHOD);
            cipher.init(Cipher.ENCRYPT_MODE, key);

            /* Encrypt the data */
            byte[] data = cipher.doFinal(fileToBytes(fileIn));

            /* Get the IV */
            AlgorithmParameters params = cipher.getParameters();
            byte[] iv = params.getParameterSpec(IvParameterSpec.class).getIV();

            /* Save the encrypted data and the iv */
            FileOutputStream out = new FileOutputStream(fileOut);
            out.write(iv);
            out.write(data);
            out.close();

            /* Encrypt the key with the pubkey */
            cipher = Cipher.getInstance(ASYMMETRIC_ENCRYPTION_METHOD);
            cipher.init(Cipher.ENCRYPT_MODE, encKey.getPublicKey());
            data = cipher.doFinal(key.getEncoded());
            
            /* Save the encrypted key */
            out = new FileOutputStream(keyFile);
            out.write(data);
            out.close();
        } catch (Exception e) {
            System.out.println("ERROR: cannot encrypt the file: " + e.getMessage());
            return false;
        }
        return true;
    }

    /**
     * Decrypt the file @param fileIn with the key stored (encrypted)
     * in @param keyFile, and save the decrypted file to @param
     * fileOut.
     * @return true on success, else false.
     */
    public boolean decrypt(String fileIn, String fileOut, String keyFile) {
        try {
            /* Read the encrypted key */
            byte[] data = fileToBytes(keyFile);

            /* Decrypt the key */
            Cipher cipher = Cipher.getInstance(ASYMMETRIC_ENCRYPTION_METHOD);
            cipher.init(Cipher.DECRYPT_MODE, decKey);
            data = cipher.doFinal(data);
            SecretKey key = new SecretKeySpec(data, 0, data.length, ENCRYPTION_ALGORITHM);

            /* Read the IV and encrypted data from the file */
            RandomAccessFile f = new RandomAccessFile(fileIn, "r");
            byte[] iv = new byte[16]; /* the IV is 16 bytes long */
            f.read(iv);
            data = new byte[(int)f.length()-16]; /* the data is stored after the IV */
            f.read(data);

            /* Initialize the cipher */
            cipher = Cipher.getInstance(ENCRYPTION_METHOD);
            cipher.init(Cipher.DECRYPT_MODE, key, new IvParameterSpec(iv));

            /* Decrypt the data */
            data = cipher.doFinal(data);

            /* Save the decrypted file */
            bytesToFile(data, fileOut);
        } catch (Exception e) {
            System.out.println("ERROR: cannot decrypt the file: " + e.getMessage());
            e.printStackTrace();
            return false;
        }
        return true;
    }

    /**
     * Compute the key for user B if A wants to share a file with
     * B. Read B's public key from @param stream. Read the key
     * from @param keyIn and store the encrypted key for B in @param
     * keyOut.
     * @return true on success, else false.
     */
    public boolean share(String keyIn, String keyOut, InputStream stream) {
        try {
            /* Read the encrypted key */
            byte[] data = fileToBytes(keyIn);

            /* Decrypt the key */
            Cipher cipher = Cipher.getInstance(ASYMMETRIC_ENCRYPTION_METHOD);
            cipher.init(Cipher.DECRYPT_MODE, decKey);
            data = cipher.doFinal(data);

            /* Read B's public key */
            Certificate encKeyB = parseCertificate(stream);
            if (encKeyB == null) {
                System.out.println("ERROR: cannot read the other user's public key");
                return false;
            }

            /* Encrypt the key */
            cipher = Cipher.getInstance(ASYMMETRIC_ENCRYPTION_METHOD);
            cipher.init(Cipher.ENCRYPT_MODE, encKeyB.getPublicKey());
            data = cipher.doFinal(data);

            /* Save the encrypted key */
            FileOutputStream out = new FileOutputStream(keyOut);
            out.write(data);
            out.close();
        } catch (Exception e) {
            System.out.println("ERROR: cannot share the file: " + e.getMessage());
            return false;
        }
        return true;
    }

    /**
     * Convert a byte array to a string representing the hexadecimal
     * representation of the byte array
     */
    private static String bytesToHex(byte[] bytes) {
        final char[] hexArray = {'0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F'};
        char[] hexChars = new char[bytes.length * 2];
        int v;
        for ( int j = 0; j < bytes.length; j++ ) {
            v = bytes[j] & 0xFF;
            hexChars[j * 2] = hexArray[v >>> 4];
            hexChars[j * 2 + 1] = hexArray[v & 0x0F];
        }
        return new String(hexChars);
    }

    /**
     * Convert a hex string to a byte array
     */
    private static byte[] hexToBytes(String s) {
        int len = s.length();
        byte[] data = new byte[len / 2];
        for (int i = 0; i < len; i += 2) {
            data[i / 2] = (byte) ((Character.digit(s.charAt(i), 16) << 4)
                                  + Character.digit(s.charAt(i+1), 16));
        }
        return data;
    }

    /**
     * Read the content of a file and return it as a byte array
     */
    private static byte[] fileToBytes(String file) throws IOException {
        RandomAccessFile f = new RandomAccessFile(file, "r");
        byte[] b = new byte[(int)f.length()];
        f.read(b);
        return b;
    }

    /**
     * Write the content of a byte array into a file
     */
    private void bytesToFile(byte[] array, String file) throws IOException {
        FileOutputStream out = new FileOutputStream(file);
        out.write(array);
        out.close();
    }
}
