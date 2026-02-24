package utils;

import javax.swing.*;
import java.awt.*;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;

/**
 * JButton avec des coins arrondis et effet de survol
 */
public class RoundedButton extends JButton {
    private int cornerRadius = 10;
    private Color baseColor;
    private Color hoverColor;
    private Color pressedColor;
    private boolean isHovered = false;
    private boolean isPressed = false;

    public RoundedButton(String text) {
        super(text);
        initialize();
    }

    public RoundedButton(String text, Color baseColor) {
        super(text);
        this.baseColor = baseColor;
        this.hoverColor = brighten(baseColor, 0.1f);
        this.pressedColor = darken(baseColor, 0.1f);
        initialize();
    }

    private void initialize() {
        setOpaque(false);
        setFocusPainted(false);
        setBorderPainted(false);
        setContentAreaFilled(false);
        setCursor(new Cursor(Cursor.HAND_CURSOR));
        setFont(new Font("Arial", Font.BOLD, 13));

        if (baseColor == null) {
            baseColor = new Color(52, 152, 219);
            hoverColor = new Color(41, 128, 185);
            pressedColor = new Color(33, 97, 140);
        }

        addMouseListener(new MouseAdapter() {
            @Override
            public void mouseEntered(MouseEvent e) {
                isHovered = true;
                repaint();
            }

            @Override
            public void mouseExited(MouseEvent e) {
                isHovered = false;
                repaint();
            }

            @Override
            public void mousePressed(MouseEvent e) {
                isPressed = true;
                repaint();
            }

            @Override
            public void mouseReleased(MouseEvent e) {
                isPressed = false;
                repaint();
            }
        });
    }

    public void setCornerRadius(int radius) {
        this.cornerRadius = radius;
        repaint();
    }

    @Override
    protected void paintComponent(Graphics g) {
        Graphics2D g2 = (Graphics2D) g.create();
        g2.setRenderingHint(RenderingHints.KEY_ANTIALIASING, RenderingHints.VALUE_ANTIALIAS_ON);

        int width = getWidth();
        int height = getHeight();

        // Couleur selon l'éstat
        Color currentColor = baseColor;
        if (isPressed) {
            currentColor = pressedColor;
        } else if (isHovered) {
            currentColor = hoverColor;
        }

        // Fond
        g2.setColor(currentColor);
        g2.fillRoundRect(0, 0, width, height, cornerRadius, cornerRadius);

        // Texte
        g2.setColor(getForeground());
        FontMetrics fm = g2.getFontMetrics();
        int textX = (width - fm.stringWidth(getText())) / 2;
        int textY = (height + fm.getAscent() - fm.getDescent()) / 2;
        g2.setFont(getFont());
        g2.drawString(getText(), textX, textY);

        g2.dispose();
    }

    private Color brighten(Color color, float factor) {
        int r = Math.min(255, (int) (color.getRed() * (1 + factor)));
        int g = Math.min(255, (int) (color.getGreen() * (1 + factor)));
        int b = Math.min(255, (int) (color.getBlue() * (1 + factor)));
        return new Color(r, g, b);
    }

    private Color darken(Color color, float factor) {
        int r = Math.max(0, (int) (color.getRed() * (1 - factor)));
        int g = Math.max(0, (int) (color.getGreen() * (1 - factor)));
        int b = Math.max(0, (int) (color.getBlue() * (1 - factor)));
        return new Color(r, g, b);
    }
}
