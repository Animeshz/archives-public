FROM alpine:3.12.4

RUN \
# ==================================== Initial setup ====================================
#
apk add --update-cache libgcc libstdc++ && \
#
apk add --virtual build-dependencies \
    autoconf \
    automake \
    bash \
    binutils \
    bison \
    bzip2 \
    flex \
    g++ \
    gawk \
    gdk-pixbuf \
    gettext \
    gettext-dev \
    git \
    gperf \
    help2man \
    intltool \
    libtool \
    linux-headers \
    lzip \
    make \
    curl \
    ncurses-dev \
    openssl \
    openssl-dev \
    p7zip \
    patch \
    perl \
    python3 \
    python3-dev \
    rsync \
    ruby \
    texinfo \
    unzip \
    wget \
    xz \
    zlib && \
#
mkdir -p /opt && \
#
#
# ==================================== Setup Windows corss compilers ====================================
#
ln -s /usr/bin/python3 /usr/bin/python && \
#
cd /opt && \
git clone https://github.com/mxe/mxe.git && \
cd mxe && \
git checkout 29bdf5b0692e1032eb1aa648f39a22f923a3d29d && \
#
sed -i \
    -e "$ a MXE_TARGETS := x86_64-w64-mingw32.shared i686-w64-mingw32.shared" \
    -e "$ a MXE_USE_CCACHE :=" \
    -e "$ a MXE_PLUGIN_DIRS := plugins/gcc10" \
    -e "$ a LOCAL_PKG_LIST := cc cmake" \
    -e "$ a .DEFAULT local-pkg-list:" \
    -e "$ a local-pkg-list: \$(LOCAL_PKG_LIST)" \
    .settings.mk && \
#
make JOBS=$(nproc) && \
#
# remove everything except usr directory
ls | grep -v usr | xargs rm -rf && \
#
#
# ==================================== Setup Linux corss compilers ====================================
#
mkdir /root/src && \
cd /opt && \
#
curl -LO http://crosstool-ng.org/download/crosstool-ng/crosstool-ng-1.24.0.tar.bz2 && \
tar -xjvf crosstool-ng-1.24.0.tar.bz2 && \
cd crosstool-ng-1.24.0 && \
#
./configure --enable-local && \
make && \
#
./ct-ng x86_64-unknown-linux-gnu && \
#
sed -i \
    -e 's/\(^CT_EXTRA_CFLAGS_FOR_BUILD=".*\)"$/\1 -D__daddr_t_defined -D__u_char_defined"/' \
    -e "$ a CT_EXPERIMENTAL=y" \
    -e "$ a CT_ALLOW_BUILD_AS_ROOT=y" \
    -e "$ a CT_ALLOW_BUILD_AS_ROOT_SURE=y" \
#   -e "$ a CT_DEBUG_CT_SAVE_STEPS=y" \
    .config && \
#
./ct-ng build.$(nproc) && \
#
cd /opt && \
rm -rf crosstool-ng-1.24.0 crosstool-ng-1.24.0.tar.bz2 && \
#
mkdir /opt/crosstool-ng && \
mv /root/x-tools/* /opt/crosstool-ng && \
rm -rf /root/x-tools && \
rm -rf /root/src && \
#
#
# ==================================== Cleanup ====================================
#
apk del build-dependencies && \
rm -rf /var/cache/apk/*